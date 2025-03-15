import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UploadTAComponent } from './upload-ta.component';

describe('UploadTAComponent', () => {
  let component: UploadTAComponent;
  let fixture: ComponentFixture<UploadTAComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UploadTAComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UploadTAComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
