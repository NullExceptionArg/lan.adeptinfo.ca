import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { loginRequest } from '../../service/dto';
import { FormsModule } from '@angular/forms';
import { UserService, LanService } from 'projects/core/src/public_api';
import { MediaMatcher } from '@angular/cdk/layout';
import { Router } from '@angular/router';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  login: loginRequest;


  constructor(
    private userService: UserService,
    private lanService: LanService,
    changeDetectorRef: ChangeDetectorRef,
    private media: MediaMatcher,
    private router: Router) {

  }

  ngOnInit(): void {
  }

  
  onSubmit() {
  }
}
