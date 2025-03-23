import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ParticipantLineComponent } from './participant-line.component';

describe('ParticipantLineComponent', () => {
  let component: ParticipantLineComponent;
  let fixture: ComponentFixture<ParticipantLineComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ParticipantLineComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ParticipantLineComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
