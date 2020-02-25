import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { loginRequest } from './dto';

@Injectable({
  providedIn: 'root'
})
export class LanapiService {
  apiUrl = "192.168.175.159:8000";
  constructor(private Http:HttpClient) { 

  }

  login(loginRequest:loginRequest){

    // const httpOptions = {
    //   headers:new HttpHeaders ({
    //     Authorization: "Bearer " + loginRequest.token
    //   })
    // }

    this.Http.post(this.apiUrl + "/oauth/token", loginRequest)
  }
}
