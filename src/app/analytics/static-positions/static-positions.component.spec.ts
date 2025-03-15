import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StaticPositionsComponent } from './static-positions.component';

describe('StaticPositionsComponent', () => {
  let component: StaticPositionsComponent;
  let fixture: ComponentFixture<StaticPositionsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [StaticPositionsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(StaticPositionsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
