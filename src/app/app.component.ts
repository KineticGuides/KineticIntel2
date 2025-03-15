import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { PageFooterComponent } from './layout/page-footer/page-footer.component';
import { PageHeaderComponent } from './layout/page-header/page-header.component';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { DataService } from './data.service';


@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, PageFooterComponent, RouterLink, PageHeaderComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})



export class AppComponent {
  title = 'BaseTemplate';
  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router
  ) { }

  logout() {
     localStorage.removeItem('uid');
     this._router.navigate(['/home']);

  }

}

