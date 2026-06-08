import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PhiliComponent } from './phili.component';

describe('PhiliComponent', () => {
  let component: PhiliComponent;
  let fixture: ComponentFixture<PhiliComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PhiliComponent],
    }).compileComponents();

    fixture = TestBed.createComponent(PhiliComponent);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
