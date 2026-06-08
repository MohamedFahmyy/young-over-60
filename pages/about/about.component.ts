import { Component } from '@angular/core';
import { AboutHeroComponent } from './component/about-hero/about-hero.component';
import { VisionComponent } from './component/vision/vision.component';
import { OfferComponent } from './component/offer/offer.component';
import { ContactMeComponent } from './component/contact-me/contact-me.component';
import { PhiliComponent } from './component/phili/phili.component';

@Component({
  selector: 'app-about',
  imports: [
    AboutHeroComponent,
    VisionComponent,
    OfferComponent,
    ContactMeComponent,
    PhiliComponent,
  ],
  templateUrl: './about.component.html',
  styleUrl: './about.component.css',
})
export class AboutComponent {}
