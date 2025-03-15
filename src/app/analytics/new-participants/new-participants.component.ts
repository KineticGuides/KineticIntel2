import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit, ViewChild } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { NgChartsModule, BaseChartDirective } from 'ng2-charts';
import { DataService } from '../../data.service'; 
import { HeySkipperComponent } from '../../widgets/hey-skipper/hey-skipper.component';
import { ChartConfiguration, ChartType } from 'chart.js';
import { ShareholderLineComponent } from '../../charts/shareholder-line/shareholder-line.component';

@Component({
  selector: 'app-new-participants',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule, HeySkipperComponent, NgChartsModule, ShareholderLineComponent],
  templateUrl: './new-participants.component.html',
  styleUrl: './new-participants.component.css'
})
export class NewParticipantsComponent  implements OnInit {

  data: any;
  message: any;
  searchText: string = '';

  constructor(
    private _activatedRoute: ActivatedRoute,
    private _dataService: DataService,
    private _router: Router,
    public http: HttpClient  // used by upload
) { }

@ViewChild('myChart', { static: false, read: BaseChartDirective })
chart: BaseChartDirective | undefined;

public updateChart() {
// If you mutated data in place:
// e.g. this.barChartData.datasets[0].data.push(100);

// Force the chart to re-check and re-render
this.chart?.update();
}

  ngOnInit(): void
  {      
      this._activatedRoute.data.subscribe(({ 
          data })=> { 
          this.data=data;
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

TApieChartType: ChartType = 'bar';

TApieChartOptions: ChartConfiguration<'pie'>['options'] = {
  responsive: true,
  maintainAspectRatio: false,
  // ...
};

TApieChartData: ChartConfiguration<'pie'>['data'] = {
  labels: [ 'Transfer Agent', 'NOBO'],
  datasets: [
    { data: [12000000, 19000000] }
  ]
};

RpieChartData: ChartConfiguration<'pie'>['data'] = {
  labels: [ 'Free Trading', 'Restricted'],
  datasets: [
    { data: [12000000, 19000000] }
  ]
};

}