<template>
  <div class="olo-tile">
    <component :is="tileComponent" :settings="tile.settings" :tile-id="tile.id" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import SectionTile from './SectionTile.vue';
import ColumnTile from './ColumnTile.vue';
import HeroTile from './HeroTile.vue';
import ContentTile from './ContentTile.vue';
import ImageTile from './ImageTile.vue';
import VideoTile from './VideoTile.vue';
import SpacerTile from './SpacerTile.vue';
import ButtonTile from './ButtonTile.vue';
import GalleryTile from './GalleryTile.vue';
import RowTile from './RowTile.vue';
import TestimonialTile from './TestimonialTile.vue';
import PricingTile from './PricingTile.vue';
import CounterTile from './CounterTile.vue';
import IconBoxTile from './IconBoxTile.vue';
import AlertTile from './AlertTile.vue';
import TeamTile from './TeamTile.vue';
import AccordionTile from './AccordionTile.vue';
import TabsTile from './TabsTile.vue';
import SocialTile from './SocialTile.vue';
import MapTile from './MapTile.vue';
import CountdownTile from './CountdownTile.vue';
import HeadlineTile from './HeadlineTile.vue';
import HtmlTile from './HtmlTile.vue';
import ListTile from './ListTile.vue';
import SlideshowTile from './SlideshowTile.vue';
import TableTile from './TableTile.vue';
import OverlayTile from './OverlayTile.vue';
import DividerTile from './DividerTile.vue';
import ProgressTile from './ProgressTile.vue';
import DescListTile from './DescListTile.vue';
import PanelTile from './PanelTile.vue';
import QuotationTile from './QuotationTile.vue';
import CodeTile from './CodeTile.vue';
import IconTile from './IconTile.vue';
import TotopTile from './TotopTile.vue';
import FragmentTile from './FragmentTile.vue';
import GridTile from './GridTile.vue';
import SwitcherTile from './SwitcherTile.vue';
import SwitcherPanelTile from './SwitcherPanelTile.vue';
import NavTile from './NavTile.vue';
import SubnavTile from './SubnavTile.vue';
import PanelSliderTile from './PanelSliderTile.vue';
import OverlaySliderTile from './OverlaySliderTile.vue';
import OverlayGridTile from './OverlayGridTile.vue';
import PopoverTile from './PopoverTile.vue';
import BreadcrumbsTile from './BreadcrumbsTile.vue';
import SearchTile from './SearchTile.vue';
import SiteLogoTile from './SiteLogoTile.vue';
import NavMenuTile from './NavMenuTile.vue';
import PostGridTile from './PostGridTile.vue';
import ProSliderTile from './ProSliderTile.vue';
import PopupTile from './PopupTile.vue';

const props = defineProps({
  tile: { type: Object, required: true },
});

const tileComponents = {
  section: SectionTile,
  column: ColumnTile,
  hero: HeroTile,
  content: ContentTile,
  image: ImageTile,
  video: VideoTile,
  spacer: SpacerTile,
  button: ButtonTile,
  gallery: GalleryTile,
  row: RowTile,
  testimonial: TestimonialTile,
  pricing: PricingTile,
  counter: CounterTile,
  iconbox: IconBoxTile,
  alert: AlertTile,
  team: TeamTile,
  accordion: AccordionTile,
  tabs: TabsTile,
  social: SocialTile,
  map: MapTile,
  countdown: CountdownTile,
  headline: HeadlineTile,
  html: HtmlTile,
  list: ListTile,
  slideshow: SlideshowTile,
  table: TableTile,
  overlay: OverlayTile,
  divider: DividerTile,
  progress: ProgressTile,
  desclist: DescListTile,
  panel: PanelTile,
  quotation: QuotationTile,
  code: CodeTile,
  icon: IconTile,
  totop: TotopTile,
  fragment: FragmentTile,
  grid: GridTile,
  switcher: SwitcherTile,
  switcherpanel: SwitcherPanelTile,
  nav: NavTile,
  subnav: SubnavTile,
  panelslider: PanelSliderTile,
  overlayslider: OverlaySliderTile,
  overlaygrid: OverlayGridTile,
  popover: PopoverTile,
  breadcrumbs: BreadcrumbsTile,
  search: SearchTile,
  sitelogo: SiteLogoTile,
  navmenu: NavMenuTile,
  postgrid: PostGridTile,
  proslider: ProSliderTile,
  popup: PopupTile,
};

const tileComponent = computed(() => {
  return tileComponents[props.tile.type] || null;
});
</script>
