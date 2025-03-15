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
  selector: 'app-global-quote',
  standalone: true,
  imports: [CommonModule, FormsModule,  NgChartsModule],
  templateUrl: './global-quote.component.html',
  styleUrl: './global-quote.component.css'
})
export class GlobalQuoteComponent  implements OnInit {

  data: any;
  message: any;
  historicalData: any;
  sortedDates: any;
  @Input() symbol: any = 'KSEZ';
  @Input() start_time: any = '';
  @Input() end_time: any = '';
  @Output() close: EventEmitter<any> = new EventEmitter<any>();

  public barChartType: ChartType = 'bar';

  apiData: any;
  daily: any;

// We'll create arrays for labels and each dataset
  v_open: any = '';
  v_close: any = '';
  v_high: any = '';
  v_low: any = '';
  v_price: any = '';
  v_volume: any = '';
  v_last_day: any = '';
  v_prev_close: any = '';
  v_change: any = '';
  v_pct_change: any = '';

  constructor(
    private stockPriceService: StockPriceService,
) { }


  ngOnInit(): void
  {      
      let uid=localStorage.getItem('uid');
      this.stockPriceService.getCurrentPrice(this.symbol).subscribe({
        next: (data) => {
          console.log(data);
          this.v_price = data['Global Quote']['05. price'];
          this.v_last_day = data['Global Quote']['07. latest trading day'];
          this.v_volume = data['Global Quote']['06. volume'];
          this.v_open = data['Global Quote']['02. open'];
          this.v_prev_close = data['Global Quote']['08. previous close'];
        },
        error: (error) => console.error('Error fetching data:', error),
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
