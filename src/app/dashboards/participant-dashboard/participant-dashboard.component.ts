import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { HeySkipperComponent } from '../../widgets/hey-skipper/hey-skipper.component';
import { ParticipantLineComponent } from '../../charts/participant-line/participant-line.component';

@Component({
  selector: 'app-participant-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, ParticipantLineComponent],
  templateUrl: './participant-dashboard.component.html',
  styleUrl: './participant-dashboard.component.css'
})
export class ParticipantDashboardComponent  implements OnInit {

  data: any;
  message: any;
  searchText: string = '';

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
          console.log("XXXXXXX");
          console.log(this.data);
      }) 
  }

  postForm(id: any): void {
  
    let formData: any = { "id": id }
    this._dataService.postData("switch-companies", formData).subscribe((data: any)=> { 
      location.reload();
  }) 

  }

  postContact() {

  }

}