import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ParticipantDecreasesComponent } from './participant-decreases.component';

describe('ParticipantDecreasesComponent', () => {
  let component: ParticipantDecreasesComponent;
  let fixture: ComponentFixture<ParticipantDecreasesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ParticipantDecreasesComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ParticipantDecreasesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
