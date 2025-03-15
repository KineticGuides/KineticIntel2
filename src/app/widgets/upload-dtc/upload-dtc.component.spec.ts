import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UploadDTCComponent } from './upload-dtc.component';

describe('UploadDTCComponent', () => {
  let component: UploadDTCComponent;
  let fixture: ComponentFixture<UploadDTCComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UploadDTCComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UploadDTCComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
