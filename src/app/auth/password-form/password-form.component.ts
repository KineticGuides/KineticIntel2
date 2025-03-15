import { Component, AfterViewInit, OnInit, Input, Output, EventEmitter  } from '@angular/core';
import { DataService } from '../../data.service';
import { CommonModule } from '@angular/common';
import { RouterLink, Router } from '@angular/router';
import { FormsModule } from '@angular/forms';

declare var $:any;



@Component({
  selector: 'app-password-form',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './password-form.component.html',
  styleUrl: './password-form.component.css'
})
export class PasswordFormComponent   implements OnInit, AfterViewInit {
  
  data: any;
  formData: any = {password: "", code: ""};
  colData: any = {country: "", languages: ""};
  keys: any;
  values: any;
  error_message: any;
  showPassword: boolean = false;

  @Input() path: any = 'template-form';
  @Input() id: any = '';
  @Input() id2: any = '';
  @Input() id3: any = '';
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  constructor(private _dataService: DataService, private router: Router) {}

  closeIt() {
   this.close.emit('N');
  }

  ngOnInit(): void {

  }

  ngAfterViewInit(): void {  


  }

  toggleOpen(m:any) {
    console.log(m)
     if (m.open=='Y') {
      m.open = 'N'
     } else {
      m.open = 'Y'
     }
  }
  
  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }
  
  postForm(): void {
    
    if (this.formData.code.length < 6 || this.formData.code.length > 6)
    {
      this.error_message = 'Pin must be 6 digits';
      //$( "div.failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
    }
    else
    {
    const userId = localStorage.getItem('u')
    this.formData['userId']=userId;
    this._dataService.postData("post-user-password", this.formData).subscribe((data: any)=> { 
      if (data.error_code=="0") {
        localStorage.setItem("uid",data.uid)
        localStorage.setItem("session",data.session)
        localStorage.setItem("userId",data.userId)
        $('#sidebar-nav').show()
        $('#sidebar-menu').show()
        $('#top-header').show()
        this.router.navigate(['/home']);

      }
      else
      {
        this.error_message = data.error_message;
        // $( "div.failure" ).fadeIn( 300 ).delay( 1500 ).fadeOut( 400 );
      }
      this.closeIt();
      console.log(this.data)
    })
  } 

  }

}

