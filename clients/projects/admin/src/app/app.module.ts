import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';

import {AppComponent} from './app.component';
import {FooterComponent} from './shared/layout/footer.component';
import {SharedModule} from './shared/shared.module';
import {AppRoutingModule} from './app-routing.module';
import {AuthModule} from './auth/auth.module';
import {LandingModule} from './landing/landing.module';
import {CoreModule} from '../../../core/src/lib/core.module';
import {CreateLanModule} from './create-lan/create-lan.module';
import {LanModule} from './lan/lan.module';
import {UserService} from '../../../core/src/lib/services/user.service';

@NgModule({
  declarations: [
    AppComponent,
    FooterComponent
  ],
  imports: [
    BrowserModule,
    CoreModule,
    SharedModule,
    AuthModule,
    LandingModule,
    CreateLanModule,
    LanModule,
    AppRoutingModule
  ],
  providers: [UserService],
  bootstrap: [AppComponent]
})
export class AppModule {
}
