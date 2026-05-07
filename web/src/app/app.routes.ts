import { Routes } from '@angular/router';
import { HomeComponent } from './home/home.component';
import { LotteriesComponent } from './lotteries/lotteries.component';
import { AboutComponent } from './about/about.component';
import { FaqComponent } from './faq/faq.component';
import { HistoryComponent } from './history/history.component';
import { ContactComponent } from './contact/contact.component';
import { PlayLotteryComponent } from './play-lottery/play-lottery.component';
import { LoginPageComponent } from './login-page/login-page.component';
import { TermsComponent } from './terms/terms.component';
import { PrivacyComponent } from './privacy/privacy.component';
import { AdminComponent } from './admin/admin.component';
import { ProfileComponent } from './profile/profile.component';
import { SettingsComponent } from './settings/settings.component';
import { WinningsComponent } from './winnings/winnings.component';
import { adminGuard } from './guards/admin.guard';
import { AdminDashboardComponent } from './admin-dashboard/admin-dashboard.component';
import { ActivityLogComponent } from './admin-dashboard/components/activity-log/activity-log.component';
import { VotingComponent } from './admin-dashboard/components/voting/voting.component';
import { ResultsComponent } from './admin-dashboard/components/results/results.component';
import { EntryComponent } from './admin-dashboard/components/entry/entry.component';
import { NotificationComponent } from './admin-dashboard/components/notification/notification.component';
import { UserComponent } from './admin-dashboard/components/user/user.component';
import { WinnerComponent } from './admin-dashboard/components/winner/winner.component';
import { ResultsDetailsComponent } from './admin-dashboard/components/results/results-details/results-details.component';
import { UserDetailsComponent } from './admin-dashboard/components/user/user-details/user-details.component';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'home', component: HomeComponent },
  { path: 'lotteries', component: LotteriesComponent },
  { path: 'about', component: AboutComponent },
  { path: 'faq', component: FaqComponent },
  { path: 'history', component: HistoryComponent },
  { path: 'contact', component: ContactComponent },
  { path: 'login', component: LoginPageComponent },
  { path: 'play-lottery', component: PlayLotteryComponent },
  { path: 'terms', component: TermsComponent },
  { path: 'privacy', component: PrivacyComponent },
  { path: 'profile', component: ProfileComponent },
  { path: 'settings', component: SettingsComponent },
  { path: 'winnings', component: WinningsComponent },
  { path: 'admin-dashboard', component: AdminDashboardComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/activity-log', component: ActivityLogComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/voting', component: VotingComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/results', component: ResultsComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/results/:id', component: ResultsDetailsComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/entry', component: EntryComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/notification', component: NotificationComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/user', component: UserComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/user/:id', component: UserDetailsComponent, canActivate: [adminGuard] },
  { path: 'admin-dashboard/winner', component: WinnerComponent, canActivate: [adminGuard] },
  { path: 'dashboard', component: AdminComponent, canActivate: [adminGuard] },
  { path: '**', redirectTo: '' }
];
