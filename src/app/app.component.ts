import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { RouterLink } from '@angular/router';
import { PageFooterComponent } from './layout/page-footer/page-footer.component';
<<<<<<< HEAD
import { TemplateHomeComponent } from './pages/template-home/template-home.component';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { DataService } from './data.service';

=======
import { DataService } from './data.service';
import { AddNotesFormComponent } from './pages/add-notes-form/add-notes-form.component';
import { ActiveCallComponent } from './pages/active-call/active-call.component';
>>>>>>> a668f6158e29e03780f56cbebe8d722be19d960f

@Component({
  selector: 'app-root',
  standalone: true,
<<<<<<< HEAD
  imports: [RouterOutlet, SidebarMenuComponent, PageHeaderComponent, PageFooterComponent, TemplateHomeComponent, RouterLink],
=======
  imports: [RouterOutlet, PageFooterComponent, RouterLink, AddNotesFormComponent, ActiveCallComponent],
>>>>>>> a668f6158e29e03780f56cbebe8d722be19d960f
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})



export class AppComponent {
  title = 'BaseTemplate';
<<<<<<< HEAD
  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router
  ) { }

  logout() {
     localStorage.removeItem('uid');
     this._router.navigate(['/home']);

  }

=======
  showCall: any = 'N';
  showText: any = 'N';

  userId: any = "";


  formData: any = { "userId": "0"}

  constructor(private _dataService: DataService) {}

  toggleMakeCall() {
    if (this.showCall=='N') {
      this.showCall = 'Y';
    } else {
      this.showCall = 'N';
    }
  }

  toggleSendText() {
    if (this.showText=='N') {
      this.showText = 'Y';
    } else {
      this.showText = 'N';
    }
  }

  logout(): void {
    this.userId = localStorage.getItem("userId")  
    this.formData['userId']=this.userId;
    this._dataService.postData("logout-user", this.formData).subscribe((data: any)=> { 
      if (data.error_code=="0") {
        localStorage.removeItem('userId')
        location.replace("/")
      }
    })
  } 

>>>>>>> a668f6158e29e03780f56cbebe8d722be19d960f
}

