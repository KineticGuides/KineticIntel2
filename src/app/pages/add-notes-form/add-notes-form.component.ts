import { Component, AfterViewInit, OnInit, Input, Output, EventEmitter  } from '@angular/core';
import { DataService } from '../../data.service';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';

declare var $:any;

@Component({
  selector: 'app-add-notes-form',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './add-notes-form.component.html',
  styleUrl: './add-notes-form.component.css'
})
export class AddNotesFormComponent  implements OnInit, AfterViewInit {
  
  data: any;
  formData: any = {country: "", languages: ""};
  colData: any = {country: "", languages: ""};
  keys: any;
  values: any;
  notes: any = '';

  @Input() path: any = 'add-notes-form';
  @Input() id: any = '';
  @Input() id2: any = '';
  @Input() id3: any = '';
  @Input() caller: any = '';
  @Input() callerSid: any = '';

  @Output() close: EventEmitter<any> = new EventEmitter<any>();
  @Output() reload: EventEmitter<any> = new EventEmitter<any>();

  constructor(private _dataService: DataService) {

  }

  closeIt() {
   this.close.emit('X');
  }

  ngOnInit(): void {

    this.id = localStorage.getItem('caller');
    this.callerSid = localStorage.getItem('CallSid');
    this._dataService.getData(this.path, this.id, this.callerSid, this.id3).subscribe((data: any)=> { 
      this.data=data;
      this.formData=data['formData'];
  }) 

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

  deleteForm() {
    if (confirm('Are you sure you want to Delete this record?')) {
      this._dataService.postData("post-notes-delete", this.formData).subscribe((data: any)=> { 
        //this.data=data;
        //this.formData=data['formData'];
        this.closeIt();
        console.log(this.data)
      }) 
    }
  }
  
  postForm(): void {
    console.log(this.data)
    this._dataService.postData("post-add-notes-form", this.formData).subscribe((data: any)=> { 
      this.reload.emit(data);
      this.formData=data['formData'];
  }) 

  }

}
