import { ComponentFixture, TestBed } from '@angular/core/testing';
import { WinningsPage } from './winnings.page';

describe('WinningsPage', () => {
  let component: WinningsPage;
  let fixture: ComponentFixture<WinningsPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(WinningsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
