import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Price14daysComponent } from './price14days.component';

describe('Price14daysComponent', () => {
  let component: Price14daysComponent;
  let fixture: ComponentFixture<Price14daysComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Price14daysComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Price14daysComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
