import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UploadNoboComponent } from './upload-nobo.component';

describe('UploadNoboComponent', () => {
  let component: UploadNoboComponent;
  let fixture: ComponentFixture<UploadNoboComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UploadNoboComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UploadNoboComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
