import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { PastDrawsPageRoutingModule } from './past-draws-routing.module';

import { PastDrawsPage } from './past-draws.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    PastDrawsPageRoutingModule
  ],
  declarations: [PastDrawsPage]
})
export class PastDrawsPageModule {}
