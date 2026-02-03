import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { WinningsPageRoutingModule } from './winnings-routing.module';

import { WinningsPage } from './winnings.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    WinningsPageRoutingModule
  ],
  declarations: [WinningsPage]
})
export class WinningsPageModule {}
