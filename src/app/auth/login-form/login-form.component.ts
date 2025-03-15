import { Component, AfterViewInit, OnInit, Input, Output, EventEmitter  } from '@angular/core';
import { DataService } from '../../data.service';
import { CommonModule } from '@angular/common';
import { RouterLink, ActivatedRoute, Router } from '@angular/router';
import { FormsModule } from '@angular/forms';

declare var $:any;

@Component({
  selector: 'app-login-form',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './login-form.component.html',
  styleUrl: './login-form.component.css'
})
export class LoginFormComponent  implements OnInit, AfterViewInit {
  
  data: any;
  formData: any = {phone: ""};
  colData: any = {country: "", languages: ""};
  keys: any;
  values: any;

  @Input() path: any = '';
  @Input() id: any = '';
  @Input() id2: any = '';
  @Input() id3: any = '';
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  constructor(private _dataService: DataService, private router: Router) {}

  closeIt() {
   this.close.emit('N');
  }

  ngOnInit(): void {
    //localStorage.removeItem('userId')
    localStorage.removeItem('CallSid')
    localStorage.removeItem('ContactName')
    localStorage.removeItem('answerCall')
    localStorage.removeItem('caller')
    localStorage.removeItem('connect-queue')
    localStorage.removeItem('connected')
    localStorage.removeItem('online')
    localStorage.removeItem('current_date')
    localStorage.removeItem('current_day')
    localStorage.removeItem('current_month')
    localStorage.removeItem('practice')
    localStorage.removeItem('doDisconnect')
    localStorage.removeItem('doReconnect')
    localStorage.removeItem('phone')
    localStorage.removeItem('incoming-call')
    localStorage.removeItem('current_practice')
    localStorage.removeItem('current_year')
    localStorage.removeItem('session')
    localStorage.removeItem('uid')
    localStorage.removeItem('uu')
    localStorage.removeItem('connect-queue')
    localStorage.removeItem('connected')
    localStorage.removeItem('online')
    localStorage.removeItem('organization')
    localStorage.removeItem('role')
    localStorage.removeItem('practice_id')
  }

  ngAfterViewInit(): void {  
     const userId=localStorage.getItem('userId')
     if (userId!==null) {
      $('#sidebar-nav').show()
      $('#sidebar-menu').show()
      $('#top-header').show()
      this.router.navigate(['/home']);
     }

  }

  validatePhoneNumber(value: string) {
    const sanitizedValue = value.replace(/\D/g, ''); // Remove non-digit characters
    this.formData['phone'] = sanitizedValue;
  }

  postForm(): void {

    this._dataService.postData("post-phone", this.formData).subscribe((data: any)=> { 
      if (data.error_code==0) {
          localStorage.setItem("uu",data.id)
          localStorage.removeItem('userId')
          localStorage.setItem('u', data.phone)
          this.router.navigate(['/p']);
          (window as any).initializeTwilioClient();
      } else {
          //  $( "div.failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
      }
  }) 

  }

}
