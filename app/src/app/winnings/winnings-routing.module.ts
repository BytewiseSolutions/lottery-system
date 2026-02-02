import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { WinningsPage } from './winnings.page';

const routes: Routes = [
  {
    path: '',
    component: WinningsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class WinningsPageRoutingModule {}
