import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { NgxPaginationModule } from 'ngx-pagination';
import { HeySkipperComponent } from '../../widgets/hey-skipper/hey-skipper.component';
import { SearchFilterPipe } from '../../search-filter.pipe';

@Component({
  selector: 'app-transfer-agent',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, SearchFilterPipe, NgxPaginationModule],
  templateUrl: './transfer-agent.component.html',
  styleUrl: './transfer-agent.component.css'
})
export class TransferAgentComponentZ  implements OnInit {

  data: any;
  message: any;
  searchText: string = '';
  p: any = 1;

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

}
