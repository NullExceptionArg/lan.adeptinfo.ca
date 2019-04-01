import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {LanComponent} from './lan.component';

const routes: Routes = [
  {
    path: 'lan',
    component: LanComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LanRoutingModule {
}
