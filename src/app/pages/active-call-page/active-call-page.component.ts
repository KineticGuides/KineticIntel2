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
import { CallDashboardComponent } from '../call-dashboard/call-dashboard.component';
import { CallNotesComponent } from '../../layout/call-notes/call-notes.component';
import { AddNotesFormComponent } from '../add-notes-form/add-notes-form.component';

declare var $:any;


@Component({
  selector: 'app-active-call-page',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, SearchFilterPipe, NgxPaginationModule, CallNotesComponent, AddNotesFormComponent],
  templateUrl: './active-call-page.component.html',
  styleUrl: './active-call-page.component.css'
})
export class ActiveCallPageComponent implements OnInit {
    caller: any;
    callSid: any;

  data: any;
  txt: any;
  message: any;
  p: any = 1;
  searchText: string = '';
  calling: any = 'N';
  showNotes: any = 'Y';
  contactUpdated: any = 'N';

  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient  // used by upload
) { }

  ngOnInit(): void
  {      
    this.showNotes='Y';
      this._activatedRoute.data.subscribe(({ 
          data })=> { 
          this.data=data;
      }) 
      $('#sidebar-nav').show()
      $('#sidebar-menu').show()
      $('#top-header').show()
      const userId = localStorage.getItem('userId')
      $('#loginstatus').html('Connected As: ' + userId)
  }

  makeCall(): void {

  }

  hangUp(): void {
    location.reload();
  }

  reload(data: any) {
    console.log(data);
     this.data = data;
  }


  callBack(m: any) {
      this.calling='Y';
      $('#outphone2').val(m.callBack);
      (window as any).makeCall2();
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

  toggleReply(m: any): void {
      if (m.reply=='Y') {
        m.reply='N';
      } else {
        m.reply='Y';
      }
  }

  toggleNotes(): void {
    this.showNotes='N';
    if (this.showNotes=='Y') {
      this.showNotes='N';
    } else {
      this.showNotes='Y';
    }
}

postContact(): void {
  this._dataService.postData("post-edit-contact", this.data.contactData).subscribe((data: any)=> { 
      this.contactUpdated = 'Y';
}) 

}


}

