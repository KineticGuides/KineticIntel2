import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditNotesFormComponent } from './edit-notes-form.component';

describe('EditNotesFormComponent', () => {
  let component: EditNotesFormComponent;
  let fixture: ComponentFixture<EditNotesFormComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EditNotesFormComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EditNotesFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
