import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit, Input } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { StorageService } from '../../localstorage.service';

@Component({
  selector: 'app-hey-skipper',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './hey-skipper.component.html',
  styleUrl: './hey-skipper.component.css'
})
export class HeySkipperComponent implements OnInit {

  data: any;
  message: any;
  @Input() title: any = 'Y';
  @Input() button: any = 'Y';
  @Input() icons: any = 'Y';
  @Input() rows: any = '3';
  @Input() card: any = 'Y';

  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient,
    private storageService: StorageService
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

    this._dataService.postSkipper("hey-skipper", formData).subscribe((data: any)=> { 
//      console.log(data.location)
//      this._router.navigate([data.location]);
//      console.log(this.data)
        if (data.action=='set-practice') this.storageService.setItem('current_practice', data.value)
        if (data.action=='set-date') {
            this.storageService.setItem('current_date', data.value.year + '-' + data.value.month + '-' + data.value.year);
            this.storageService.setItem('current_year', data.value.year);
            this.storageService.setItem('current_month', data.value.month);
            this.storageService.setItem('current_day', data.value.day);
        }
  }) 

  }


}
