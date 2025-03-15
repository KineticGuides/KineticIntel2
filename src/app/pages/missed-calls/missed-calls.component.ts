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
  selector: 'app-missed-calls',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, SearchFilterPipe, NgxPaginationModule],
  templateUrl: './missed-calls.component.html',
  styleUrl: './missed-calls.component.css'
})
export class MissedCallsComponent  implements OnInit {

  data: any;
  message: any;
  p: any = 1;
  searchText: string = '';

  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient  // used by upload
) { }

  ngOnInit(): void
  {      
    $('#sidebar-nav').show()
    $('#sidebar-menu').show()
    $('#top-header').show()
      this._activatedRoute.data.subscribe(({ 
          data })=> { 
          this.data=data;
      }) 
      const userId = localStorage.getItem('userId')
  }

  postForm(): void {
  
    let formData: any = { "message": this.message }

    this._dataService.postData("hey-skipper", formData).subscribe((data: any)=> { 
      console.log(data.location)
      this._router.navigate([data.location]);
      console.log(this.data)
  }) 

  }

  hideCall(m: any): void {
  
    if (confirm('Are you sure you want to delete this record?')) {
      this._dataService.postData("hide-call", m).subscribe((data: any)=> { 
        this.data=data;
    }) 
    }
  }


}
