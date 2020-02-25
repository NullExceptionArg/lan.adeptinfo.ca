import { Component, OnInit } from '@angular/core';
import { loginRequest } from '../service/dto';
import {FormsModule} from '@angular/forms';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  login:loginRequest;


  constructor() { }

  ngOnInit(): void {
  }
  onSubmit(){

  }
}
