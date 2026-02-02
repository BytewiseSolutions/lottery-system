import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule } from '@angular/forms';
import { HomePage } from './home.page';
import { EntriesComponent } from '../components/entries/entries.component';
import { ResultsComponent } from '../components/results/results.component';
import { ProfileComponent } from '../components/profile/profile.component';

import { HomePageRoutingModule } from './home-routing.module';


@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    HomePageRoutingModule
  ],
  declarations: [HomePage, EntriesComponent, ResultsComponent, ProfileComponent]
})
export class HomePageModule {}
