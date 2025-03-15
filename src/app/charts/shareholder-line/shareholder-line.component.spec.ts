import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ShareholderLineComponent } from './shareholder-line.component';

describe('ShareholderLineComponent', () => {
  let component: ShareholderLineComponent;
  let fixture: ComponentFixture<ShareholderLineComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ShareholderLineComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ShareholderLineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
