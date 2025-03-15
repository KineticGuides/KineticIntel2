import { ComponentFixture, TestBed } from '@angular/core/testing';

import { GlobalQuoteComponent } from './global-quote.component';

describe('GlobalQuoteComponent', () => {
  let component: GlobalQuoteComponent;
  let fixture: ComponentFixture<GlobalQuoteComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [GlobalQuoteComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(GlobalQuoteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
