import { Component, OnInit, OnDestroy, AfterViewInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { WebSocketService } from '../../web-sockets.service';
import { DataService } from '../../data.service';
import { CallDashboardComponent } from '../../pages/telecom/call-dashboard/call-dashboard.component';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { StorageService } from '../../localstorage.service';

@Component({
  selector: 'app-page-header',
  standalone: true,
  imports: [CallDashboardComponent, FormsModule],
  templateUrl: './page-header.component.html',
  styleUrl: './page-header.component.css'
})
export class PageHeaderComponent  implements OnInit, OnDestroy {
  data:any;

  private subscription!: Subscription;

  call_alert_open: any = 'N';
  caller: any = '';
  user_name: any = '';
  online: any = 'N';
  live: any = 'N';
  openedWindow: Window | null = null;  
  Sid: any = '';
  ParentSid: any = '';

  message: any;


  constructor(
    private _dataService: DataService,
    private _router: Router,
    private storageService: StorageService = new StorageService
) { }

ngOnInit(): void {

  let formData: any = { id: "0" }

  this._dataService.postData("get-page-header", formData).subscribe((data: any)=> {
    this.data=data;
    console.log("ngOnInit")
    console.log(this.data)
})

}
  
  doQueue() {
    localStorage.setItem('connect-queue','Y')   
  } 

  postLogout() {
    localStorage.removeItem("uu");
    localStorage.removeItem("uid");
    localStorage.removeItem("role");
    localStorage.removeItem("session");
    location.replace("https://google.com");
  }

  ngOnDestroy(): void {
    this.subscription.unsubscribe();
  }
}