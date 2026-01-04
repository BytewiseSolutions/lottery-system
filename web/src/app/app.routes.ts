import { Routes } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { LotteriesComponent } from './lotteries/lotteries.component';
import { ResultsComponent } from './results/results.component';
import { AboutComponent } from './about/about.component';
import { FaqComponent } from './faq/faq.component';
import { HistoryComponent } from './history/history.component';
import { ContactComponent } from './contact/contact.component';
import { PlayLotteryComponent } from './play-lottery/play-lottery.component';
import { TermsComponent } from './terms/terms.component';
import { PrivacyComponent } from './privacy/privacy.component';
import { AdminComponent } from './admin/admin.component';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'home', component: HomeComponent },
  { path: 'lotteries', component: LotteriesComponent },
  { path: 'results', component: ResultsComponent },
  { path: 'about', component: AboutComponent },
  { path: 'faq', component: FaqComponent },
  { path: 'history', component: HistoryComponent },
  { path: 'contact', component: ContactComponent },
  { path: 'play-lottery', component: PlayLotteryComponent },
  { path: 'terms', component: TermsComponent },
  { path: 'privacy', component: PrivacyComponent },
  { path: 'dashboard', component: AdminComponent },
  { path: '**', redirectTo: '' }
];
