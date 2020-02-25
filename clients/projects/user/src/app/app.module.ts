import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { RouterModule, Routes } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { HomeComponent } from './home/home.component';
import { CountdownComponent } from './countdown/countdown/countdown.component';
import { DatecountdownComponent } from './countdown/datecountdown/datecountdown.component';
import { LoginComponent } from './login/login.component';
import { FormsModule } from '@angular/forms';

const routes: Routes = [
  {path:"Home", component:HomeComponent},
  {path:"login", component:LoginComponent},
  {path:"**", redirectTo:"/Home"}
];

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    CountdownComponent,
    DatecountdownComponent,
    LoginComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    RouterModule.forRoot(
      routes
    ),
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
