import { NgModule }					from '@angular/core';
import { RouterModule, Routes}		from '@angular/router';

// import { AppComponent } 		from './app.component';
import { IndexComponent }		from './home/index/index.component';
import { GoodsComponent }		from './home/goods/goods/goods.component';

const routes: Routes = [
	{ path: '', redirectTo:'app-index', pathMatch:'full' },
	// { path: '', component: AppComponent },
	{ path: 'goods', loadChildren: 'app/home/goods/goods.module#GoodsModule' }
];

@NgModule({
	imports: 		[ RouterModule.forRoot(routes) ],
	exports: 		[ RouterModule ]
})
export class AppRoutingModule { }