import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { HeySkipperComponent } from '../../widgets/hey-skipper/hey-skipper.component';
import { NgChartsModule, BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration, ChartType } from 'chart.js';
import { StockPriceService } from '../../stock-price.service';
import { Price14daysComponent } from '../../charts/price14days/price14days.component';
import { GlobalQuoteComponent } from '../../charts/global-quote/global-quote.component';
import { SearchFilterPipe } from '../../search-filter.pipe';
import { NgxPaginationModule } from 'ngx-pagination';

declare var $:any;

@Component({
  selector: 'app-home-page',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, NgChartsModule, Price14daysComponent, GlobalQuoteComponent,SearchFilterPipe, NgxPaginationModule],
  templateUrl: './home-page.component.html',
  styleUrl: './home-page.component.css'
})
export class HomePageComponent  implements OnInit {

  data: any;
  txt: any;
  message: any;
  symbol = 'KSEZ';
  historicalData: any;
  sortedDates: any;

  apiData: any;
  daily: any;

  @ViewChild('myChart', { static: false, read: BaseChartDirective })
  chart: BaseChartDirective | undefined;

public updateChart() {
  // If you mutated data in place:
  // e.g. this.barChartData.datasets[0].data.push(100);

  // Force the chart to re-check and re-render
  this.chart?.update();
}
  p: any = 1;
  searchText: string = '';
  calling: any = 'N';

  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient  // used by upload
) { }


  ngOnInit(): void
  {      
      let uid=localStorage.getItem('uid');
      console.log(uid);
      if (uid===undefined||uid==='0'||uid===null) {
        this._router.navigate(['/login']);
      }
      this._activatedRoute.data.subscribe(({ 
          data })=> { 
          this.data=data;
      })
  }

  makeCall(): void {

  }

  hangUp(): void {
    location.reload();
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

  toggleSMS(m: any): void {
    if (m.sms=='Y') {
      m.sms='N';
    } else {
      m.sms='Y';
    }

}

}
