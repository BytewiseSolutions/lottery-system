import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { PastDrawsPage } from './past-draws.page';

const routes: Routes = [
  {
    path: '',
    component: PastDrawsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class PastDrawsPageRoutingModule {}
