import { Component, AfterViewInit, OnInit, Input, Output, EventEmitter  } from '@angular/core';
import { DataService } from '../../data.service';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
declare var $:any;

@Component({
  selector: 'app-contact-form',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './contact-form.component.html',
  styleUrl: './contact-form.component.css'
})
export class ContactFormComponent  implements OnInit, AfterViewInit {
  
  data: any;
  formData: any = {country: "", languages: ""};
  colData: any = {country: "", languages: ""};
  keys: any;
  values: any;

  @Input() path: any = 'contact-form';
  @Input() id: any = '';
  @Input() id2: any = '';
  @Input() id3: any = '';
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  constructor(private _dataService: DataService) {

  }

  closeIt() {
   this.close.emit('N');
  }

  ngOnInit(): void {

    $('#sidebar-nav').show()
    $('#sidebar-menu').show()
    $('#top-header').show()
    
    this._dataService.getData(this.path, this.id, this.id2, this.id3).subscribe((data: any)=> { 
      this.data=data;
      this.formData=data['formData'];
      this.colData=data['colData'];
      this.keys = Object.keys(this.formData);
      this.values = Object.entries(this.formData);
      console.log(this.data['formData'])
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
      this._dataService.postData("post-contact-delete", this.formData).subscribe((data: any)=> { 
        //this.data=data;
        //this.formData=data['formData'];
        this.closeIt();
        console.log(this.data)
      }) 
    }
  }
  
  postForm(): void {
    this._dataService.postData("post-contact-form", this.formData).subscribe((data: any)=> { 
      //this.data=data;
      //this.formData=data['formData'];
      this.closeIt();
      console.log(this.data)
  }) 

  }

}
