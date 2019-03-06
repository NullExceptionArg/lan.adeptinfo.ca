import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {HTTP_INTERCEPTORS} from '@angular/common/http';
import {ApiService} from './services/api.service';
import {HttpTokenInterceptor} from './inteceptors/http.token.interceptor';
import {JwtService} from './services/jwt.service';
import {UserService} from './services/user.service';
import {AuthGuard} from './services/auth-guard.service';

@NgModule({
  imports: [
    CommonModule
  ],
  providers: [
    {provide: HTTP_INTERCEPTORS, useClass: HttpTokenInterceptor, multi: true},
    ApiService,
    AuthGuard,
    JwtService,
    UserService
  ],
  declarations: []
})
export class CoreModule {
}
