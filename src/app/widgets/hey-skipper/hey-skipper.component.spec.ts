import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HeySkipperComponent } from './hey-skipper.component';

describe('HeySkipperComponent', () => {
  let component: HeySkipperComponent;
  let fixture: ComponentFixture<HeySkipperComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HeySkipperComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HeySkipperComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
