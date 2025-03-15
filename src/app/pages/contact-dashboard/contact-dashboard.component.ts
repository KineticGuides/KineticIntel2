import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { HeySkipperComponent } from '../../widgets/hey-skipper/hey-skipper.component';
import { SearchFilterPipe } from '../../search-filter.pipe';
import { NgxPaginationModule } from 'ngx-pagination';

declare var $:any;

@Component({
  selector: 'app-contact-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, SearchFilterPipe, NgxPaginationModule],
  templateUrl: './contact-dashboard.component.html',
  styleUrl: './contact-dashboard.component.css'
})
export class ContactDashboardComponent implements OnInit {

  data: any;
  message: any;
  contactUpdated: any = 'N';
  p: any = 1;
  ipp: any = 3;
  searchText: any = '';

  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient  // used by upload
  ) { }

  showMore() {
    this.ipp = 10;
  }
  showLess() {
    this.ipp = 3;
  }
  ngOnInit(): void
  {  
    this.contactUpdated='N';
    $('#sidebar-nav').show()
    $('#sidebar-menu').show()
    $('#top-header').show()    
    $('#button-bar').hide()    
      this._activatedRoute.data.subscribe(({ 
          data })=> { 
          this.data=data;
      }) 
  }

  postForm(): void {
  
    let formData: any = { "message": this.message }

    this._dataService.postData("hey-skipper", formData).subscribe((data: any)=> { 
      console.log(data.location)
      this._router.navigate([data.location]);
      console.log(this.data)
  }) 
  }
  postContact(): void {
    this._dataService.postData("post-edit-contact", this.data.contactData).subscribe((data: any)=> { 
        this.contactUpdated = 'Y';
  }) 
}
}