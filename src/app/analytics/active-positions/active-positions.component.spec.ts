import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ActivePositionsComponent } from './active-positions.component';

describe('ActivePositionsComponent', () => {
  let component: ActivePositionsComponent;
  let fixture: ComponentFixture<ActivePositionsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ActivePositionsComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ActivePositionsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
