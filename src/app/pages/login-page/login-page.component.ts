import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { NgxPaginationModule } from 'ngx-pagination';
import { CalendarModule } from '../../calendar/calendar.module';
import { ProviderCalendarModule } from '../../provider-calendar/provider-calendar.module';
import { SearchFilterPipe } from '../../search-filter.pipe';

@Component({
  selector: 'app-login-page',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, SearchFilterPipe, NgxPaginationModule],
  templateUrl: './login-page.component.html',
  styleUrl: './login-page.component.css'
})
export class LoginPageComponent   implements OnInit {

  data: any;
  message: any;
  searchText: string = '';
  p: any = 1;
  error: string = 'N';


  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient  // used by upload
) { }

  ngOnInit(): void
  {      
      this._activatedRoute.data.subscribe(({ 
          data })=> { 
          this.data=data;
          console.log(this.data.formData);
      }) 
  }

  switchUserGeneral(m: any): void { 
    let formData: any = { "member_id": m.id }
    this._dataService.postData("switch-member", formData).subscribe((data: any)=> { 
    this._router.navigate(['/home']);
  }) 

  }

  postForm(): void {
  
    this._dataService.postData("post-login", this.data.formData).subscribe((data: any)=> { 
      console.log(data);
      if (data.error==0) {
        this.error='N';
        localStorage.setItem('uid', data.user.id);
        localStorage.setItem('chat_id', data.user.chat_id);
        localStorage.setItem('hash', data.user.hash);
        localStorage.setItem('role', data.user.role);
        this._router.navigate(['/home']);
      } else {
        this.error='Y';
      }
      console.log(this.data)
  }) 

  }


}
