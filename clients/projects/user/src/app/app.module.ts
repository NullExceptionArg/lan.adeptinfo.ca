import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { RouterModule, Routes } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { HomeComponent } from './home/home.component';
import { CountdownComponent } from './countdown/countdown/countdown.component';
import { DatecountdownComponent } from './countdown/datecountdown/datecountdown.component';
import { FormsModule } from '@angular/forms';
import { AuthModule } from './auth/auth.module';
import { NotfoundComponent } from './notfound/notfound.component';
import { UserService, JwtService } from 'projects/core/src/public_api';
import { ApiService } from 'projects/core/src/lib/services/api.service';

const routes: Routes = [
  { path: "Home", component: HomeComponent },
  { path: "auth", loadChildren: () => import('./auth/auth.module').then(m => m.AuthModule) },
  { path: "**", redirectTo: "/Home" }
];

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    CountdownComponent,
    DatecountdownComponent,
    NotfoundComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    RouterModule.forRoot(
      routes
    ),
    AuthModule,
  ],
  providers: [UserService,ApiService,JwtService],
  bootstrap: [AppComponent]
})
export class AppModule { }
