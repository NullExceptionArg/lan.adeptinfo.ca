import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LoginComponent } from './login/login.component';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule } from "@angular/forms";
import { RegisterComponent } from './register/register.component';
import { core } from '@angular/compiler';
import { SharedModule } from 'projects/admin/src/app/shared/shared.module';
import { LogoutComponent } from './logout/logout.component';


const appRoutes: Routes = [
  { path: 'auth/login', component: LoginComponent },
  { path: 'auth/register', component: RegisterComponent },
  { path: 'auth/logout', component: LogoutComponent },
  { path: 'auth/**', component:LoginComponent}
]
@NgModule({
  declarations: [LoginComponent, RegisterComponent, LogoutComponent],
  imports: [
    CommonModule,
    FormsModule,
    SharedModule,
    RouterModule.forChild(appRoutes)
  ]
})
export class AuthModule { }
