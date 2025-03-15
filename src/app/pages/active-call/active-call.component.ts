import { Component } from '@angular/core';
import { Router } from '@angular/router';

declare var $:any;

declare global {
  interface Window {
    angularNavigate: (caller: any, sid: any) => void;
  }
}

@Component({
  selector: 'app-active-call',
  standalone: true,
  imports: [],
  templateUrl: './active-call.component.html',
  styleUrl: './active-call.component.css'
})
export class ActiveCallComponent {
  constructor(private router: Router) {
    window.angularNavigate = this.navigateToComponent.bind(this);
  }

  navigateToComponent(caller: any, sid: any) {
    this.router.navigate(['/call-in-progress',caller,sid]);
  }


}
