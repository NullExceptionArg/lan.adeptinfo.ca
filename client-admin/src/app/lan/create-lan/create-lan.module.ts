import {NgModule} from '@angular/core';
import {SharedModule} from '../../shared/shared.module';
import {CreateLanComponent} from './create-lan.component';
import {CreateLanDetailsComponent} from './details/create-lan-details.component';

@NgModule({
  imports: [
    SharedModule
  ],
  declarations: [
    CreateLanComponent,
    CreateLanDetailsComponent
  ]
})
export class CreateLanModule {
}
