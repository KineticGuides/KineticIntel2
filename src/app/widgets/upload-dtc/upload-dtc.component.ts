import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-upload-dtc',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './upload-dtc.component.html',
  styleUrl: './upload-dtc.component.css'
})
export class UploadDTCComponent  {

  selectedFile: File | null = null;
  securityId: string = '';
  recordDate: string = '';

  constructor(private http: HttpClient) {}

  onFileSelected(event: Event) {
    const element = event.currentTarget as HTMLInputElement;
    let fileList: FileList | null = element.files;
    if (fileList) {
      this.selectedFile = fileList[0];
    } else {
      this.selectedFile = null;
    }
  }

  onSubmit() {
    if (this.selectedFile) {
      const formData = new FormData();
      formData.append('file', this.selectedFile, this.selectedFile.name);
      formData.append('security_id', this.securityId); 
      formData.append('record_date', this.recordDate); 
      this.http.post('http://localhost:8888/ksa_upload_ta_stc.php', formData).subscribe((data)=> { 
        console.log(data);
    })}
  }
}