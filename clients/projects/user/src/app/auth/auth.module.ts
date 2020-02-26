import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { LoginComponent } from './login/login.component';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule } from "@angular/forms";

const appRoutes: Routes = [
  { path: 'auth/login', component: LoginComponent },
  { path: 'auth/register', component: LoginComponent },
  { path: 'auth/logout', component: LoginComponent },
  { path: 'auth/**', component:LoginComponent}
]
@NgModule({
  declarations: [LoginComponent],
  imports: [
    CommonModule,
    FormsModule,
    RouterModule.forChild(appRoutes)
  ]
})
export class AuthModule { }
