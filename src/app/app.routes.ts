import { Routes } from '@angular/router';
import { ResolverService } from './resolver.service';
import { TemplateListComponent } from './pages/template-list/template-list.component';
import { UserListComponent } from './pages/crud/user/user-list/user-list.component';
import { HomePageComponent } from './pages/home-page/home-page.component';
import { ParticipantsComponent } from './pages/participants/participants.component';
import { ShareholdersComponent } from './pages/shareholders/shareholders.component';
import { AnalyticsComponent } from './pages/analytics/analytics.component';
import { InsightsComponent } from './pages/insights/insights.component';
import { DocumentsComponent } from './pages/documents/documents.component';
import { FilingsComponent } from './pages/filings/filings.component';
import { AdminComponent } from './pages/admin/admin.component';
import { SettingsComponent } from './pages/settings/settings.component';
import { CompaniesComponent } from './pages/companies/companies.component';
import { CompanySetupComponent } from './pages/company-setup/company-setup.component';
import { ShareholderDashboardComponent } from './dashboards/shareholder-dashboard/shareholder-dashboard.component';
import { ParticipantDashboardComponent } from './dashboards/participant-dashboard/participant-dashboard.component';
import { LoginPageComponent } from './pages/login-page/login-page.component';
import { StaticPositionsComponent } from './analytics/static-positions/static-positions.component';
import { ActivePositionsComponent } from './analytics/active-positions/active-positions.component';
import { BuyersComponent } from './analytics/buyers/buyers.component';
import { SellersComponent } from './analytics/sellers/sellers.component';
import { NewPositonsComponent } from './analytics/new-positons/new-positons.component';
import { LostPositonsComponent } from './analytics/lost-positons/lost-positons.component';
import { TransferAgentComponent } from './analytics/transfer-agent/transfer-agent.component';
import { ActiveParticipantsComponent } from './analytics/active-participants/active-participants.component';
import { ParticipantDecreasesComponent } from './analytics/participant-decreases/participant-decreases.component';
import { ParticipantIncreasesComponent } from './analytics/participant-increases/participant-increases.component';
import { NewParticipantsComponent } from './analytics/new-participants/new-participants.component';
import { LostParticipantsComponent } from './analytics/lost-participants/lost-participants.component';

export const routes: Routes = [
    { path: '', component: HomePageComponent, resolve: { data: ResolverService} },
    { path: 'login', component: LoginPageComponent, resolve: { data: ResolverService} },
    { path: 'home', component: HomePageComponent, resolve: { data: ResolverService} },
    { path: 'participants', component: ParticipantsComponent, resolve: { data: ResolverService}  },
    { path: 'participant-dashboard/:id', component: ParticipantDashboardComponent, resolve: { data: ResolverService}  },
    { path: 'shareholders', component: ShareholdersComponent, resolve: { data: ResolverService} },
    { path: 'analytics', component: AnalyticsComponent, resolve: { data: ResolverService} },
    { path: 'insights', component: InsightsComponent, resolve: { data: ResolverService} },
    { path: 'documents', component: DocumentsComponent, resolve: { data: ResolverService} },
    { path: 'filings', component: FilingsComponent, resolve: { data: ResolverService} },
    { path: 'admin', component: AdminComponent, resolve: { data: ResolverService} },
    { path: 'settings', component: SettingsComponent, resolve: { data: ResolverService} },
    { path: 'companies', component: CompaniesComponent, resolve: { data: ResolverService} },
    { path: 'company', component: CompanySetupComponent, resolve: { data: ResolverService} },
    { path: 'static-positions', component: StaticPositionsComponent, resolve: { data: ResolverService} },
    { path: 'active-positions', component: ActivePositionsComponent, resolve: { data: ResolverService} },
    { path: 'buyers', component: BuyersComponent, resolve: { data: ResolverService} },
    { path: 'sellers', component: SellersComponent, resolve: { data: ResolverService} },
    { path: 'new-positions', component: NewPositonsComponent, resolve: { data: ResolverService} },
    { path: 'lost-positions', component: LostPositonsComponent, resolve: { data: ResolverService} },
    { path: 'transfer-agent', component: TransferAgentComponent, resolve: { data: ResolverService} },
    { path: 'active-participants', component: ActiveParticipantsComponent, resolve: { data: ResolverService} },
    { path: 'participant-decreases', component: ParticipantDecreasesComponent, resolve: { data: ResolverService} },
    { path: 'participant-increases', component: ParticipantIncreasesComponent, resolve: { data: ResolverService} },
    { path: 'new-participants', component: NewParticipantsComponent, resolve: { data: ResolverService} },
    { path: 'lost-participants', component: LostParticipantsComponent, resolve: { data: ResolverService} },
    { path: 'shareholder-dashboard/:id', component: ShareholderDashboardComponent, resolve: { data: ResolverService} },
    { path: 'template-list', component: TemplateListComponent, resolve: { data: ResolverService}  }
];
