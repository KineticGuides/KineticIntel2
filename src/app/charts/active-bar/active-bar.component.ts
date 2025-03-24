import { CommonModule } from '@angular/common';
import { Component, OnDestroy, OnInit, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { ActivatedRoute, Router, RouterModule, RouterLink } from '@angular/router';
import { Subject, takeUntil } from 'rxjs';
import { FormsModule,  FormGroup, FormControl, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { DataService } from '../../data.service'; 
import { NgChartsModule, BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration, ChartType } from 'chart.js';
import { StockPriceService } from '../../stock-price.service';

@Component({
  selector: 'app-active-bar',
  standalone: true,
  imports: [CommonModule, FormsModule,  NgChartsModule],
  templateUrl: './active-bar.component.html',
  styleUrl: './active-bar.component.css'
})
export class ActiveBarComponent  implements OnInit {

  data: any;
  message: any;
  historicalData: any;
  sortedDates: any;
  @Input() symbol: any = 'KSEZ';
  @Input() id: any = 0;
  @Input() id2: any =0;
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  public barChartType: ChartType = 'bar';

  apiData: any;
  daily: any;

  labels: any = [];
  static: number[] = [];
  shareholders: number[] = [];

  @ViewChild('myChart', { static: false, read: BaseChartDirective })
  chart: BaseChartDirective | undefined;

public updateChart() {
  this.chart?.update();
}

constructor(
  private _dataService: DataService,
) { }

public barChartOptions: ChartConfiguration<'bar'>['options'] = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      stacked: false
    },
    y: {
      beginAtZero: true,
      ticks: {
        callback: (value: number | string) => {
          return value; // You can format here (e.g., with commas or currency)
        }
      }
    }
  }
};


// Only one dataset for Price

public barChartData: ChartConfiguration<'bar'>['data'] = {
  labels: this.labels,
  datasets: [
    {
      label: 'Active',
      data: this.static,
      backgroundColor: 'rgba(0, 123, 255, 0.7)'
    },
    {
      label: 'Shareholders',
      data: this.shareholders,
      backgroundColor: 'rgba(255, 99, 132, 0.7)'
    }
  ]
};

  ngOnInit(): void
  {      
      let uid=localStorage.getItem('uid');
      this._dataService.getData("get-active-graph", this.id, this.id2, this.symbol).subscribe((data: any)=> { 
        this.data=data;
        this.labels=data.labels;
        this.static=data.static;
        this.shareholders=data.shareholders;  
        this.barChartData.labels = [ ...this.labels ];
        this.barChartData.datasets[1].data = [ ...this.shareholders ];
        this.barChartData.datasets[0].data = [ ...this.static ];
        this.updateChart();
    });
  }

  formatYYYYMMDDtoMMDD(dateStr: string): string {
    // dateStr is "YYYY-MM-DD"
    const [year, month, day] = dateStr.split('-');
    return `${month}/${day}`; // e.g., "03/07"
  }

  transformData(timeSeries: any): any[] {
    return Object.keys(timeSeries).map((date) => {
      return {
        date: date,
        open: +timeSeries[date]['1. open'],
        high: +timeSeries[date]['2. high'],
        low: +timeSeries[date]['3. low'],
        close: +timeSeries[date]['4. close'],
        volume: +timeSeries[date]['6. volume'],
      };
    });
  }

  getLast5Days(data: any[]): any[] {
    // Sort descending by date, then slice top 5
    return data.sort((a, b) => {
      return new Date(b.date).getTime() - new Date(a.date).getTime();
    }).slice(0, 5);
  }

// FS34H3MQECXBP7UH

}