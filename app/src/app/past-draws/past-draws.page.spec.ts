import { ComponentFixture, TestBed } from '@angular/core/testing';
import { PastDrawsPage } from './past-draws.page';

describe('PastDrawsPage', () => {
  let component: PastDrawsPage;
  let fixture: ComponentFixture<PastDrawsPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(PastDrawsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
