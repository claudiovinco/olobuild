<template>
  <div class="olo-tile" :style="tileWrapStyle">
    <component :is="tileComponent" :settings="tile.settings" :tile-id="tile.id" :tile="tile" />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import SectionTile from './SectionTile.vue';
import ColumnTile from './ColumnTile.vue';
import HeroTile from './HeroTile.vue';
import HeroSplitTile from './HeroSplitTile.vue';
import AudioHeroTile from './AudioHeroTile.vue';
import SectionHeaderTile from './SectionHeaderTile.vue';
import InfoCardsTile from './InfoCardsTile.vue';
import WorkListTile from './WorkListTile.vue';
import WorkGridTile from './WorkGridTile.vue';
import StatStripTile from './StatStripTile.vue';
import HoursStripTile from './HoursStripTile.vue';
import HoverListTile from './HoverListTile.vue';
import LookbookMixerTile from './LookbookMixerTile.vue';
import CategoryRailTile from './CategoryRailTile.vue';
import BeforeAfterTile from './BeforeAfterTile.vue';
import TripFinderTile from './TripFinderTile.vue';
import MaskedVideoHeroTile from './MaskedVideoHeroTile.vue';
import SearchHeroTile from './SearchHeroTile.vue';
import SmearHeroTile from './SmearHeroTile.vue';
import PhotoCoverTile from './PhotoCoverTile.vue';
import MastheadTile from './MastheadTile.vue';
import MatchFixturesTile from './MatchFixturesTile.vue';
import ShowcaseGridTile from './ShowcaseGridTile.vue';
import ProductGridTile from './ProductGridTile.vue';
import AnnouncementBarTile from './AnnouncementBarTile.vue';
import IntroSplitTile from './IntroSplitTile.vue';
import MediaCtaTile from './MediaCtaTile.vue';
import ImageHeroTile from './ImageHeroTile.vue';
import GlowHeroTile from './GlowHeroTile.vue';
import TerminalHeroTile from './TerminalHeroTile.vue';
import OloxNavTile from './OloxNavTile.vue';
import OloxHeroTile from './OloxHeroTile.vue';
import OloxMarqueeTile from './OloxMarqueeTile.vue';
import OloxCardsTile from './OloxCardsTile.vue';
import OloxStickyTile from './OloxStickyTile.vue';
import OloxPricingTile from './OloxPricingTile.vue';
import OloxStatementTile from './OloxStatementTile.vue';
import OloxListTile from './OloxListTile.vue';
import OloxLessonsTile from './OloxLessonsTile.vue';
import OloxQuizTile from './OloxQuizTile.vue';
import OloxBannerTile from './OloxBannerTile.vue';
import OloxFootTile from './OloxFootTile.vue';
import OloxPageFxTile from './OloxPageFxTile.vue';
import OloxManualTile from './OloxManualTile.vue';
import OloxPanelTile from './OloxPanelTile.vue';
import OloxRailTile from './OloxRailTile.vue';
import OloxSceneTile from './OloxSceneTile.vue';
import CoverdotsTile from './CoverdotsTile.vue';
import BottombarTile from './BottombarTile.vue';
import PagelightTile from './PagelightTile.vue';
import OloxHomeTile from './OloxHomeTile.vue';
import ProductHeroTile from './ProductHeroTile.vue';
import NorthVideoHeroTile from './NorthVideoHeroTile.vue';
import NorthQuoteSliderTile from './NorthQuoteSliderTile.vue';
import FeaturedStoryTile from './FeaturedStoryTile.vue';
import GlowGalleryTile from './GlowGalleryTile.vue';
import ChatHeroTile from './ChatHeroTile.vue';
import ProductCardsTile from './ProductCardsTile.vue';
import StepTimelineTile from './StepTimelineTile.vue';
import ProcessStepsTile from './ProcessStepsTile.vue';
import CtaBannerTile from './CtaBannerTile.vue';
import TrustStripTile from './TrustStripTile.vue';
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
import BadgeTile from './BadgeTile.vue';
import TeamTile from './TeamTile.vue';
import AccordionTile from './AccordionTile.vue';
import ProjectorTile from './ProjectorTile.vue';
import FinderTile from './FinderTile.vue';
import BuilderTile from './BuilderTile.vue';
import MixerTile from './MixerTile.vue';
import ScheduleTile from './ScheduleTile.vue';
import HotspotsTile from './HotspotsTile.vue';
import ScalerTile from './ScalerTile.vue';
import TimezoneTile from './TimezoneTile.vue';
import AvailabilityTile from './AvailabilityTile.vue';

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
import MegaMenuTile from './MegaMenuTile.vue';
import OloHeaderTile from './OloHeaderTile.vue';
import InnerColumnsTile from './InnerColumnsTile.vue';
import StudioHeroTile from './StudioHeroTile.vue';
import FilmReelTile from './FilmReelTile.vue';
import ScrubTextTile from './ScrubTextTile.vue';
import ThemeDemosTile from './ThemeDemosTile.vue';
import EvoNotesTile from './EvoNotesTile.vue';
import IconTabsTile from './IconTabsTile.vue';
import TimelineTile from './TimelineTile.vue';
import FlipCardTile from './FlipCardTile.vue';
import ImgCompareTile from './ImgCompareTile.vue';
import MarqueeTile from './MarqueeTile.vue';
import ToggleBtnTile from './ToggleBtnTile.vue';
import FormTile from './FormTile.vue';
import CalendarTile from './CalendarTile.vue';
import BookingTile from './BookingTile.vue';
import ServiceListTile from './ServiceListTile.vue';
import ServiceInfoTile from './ServiceInfoTile.vue';
import ServiceHeroTile from './ServiceHeroTile.vue';
import ServiceStatsTile from './ServiceStatsTile.vue';
import ServicePricesTile from './ServicePricesTile.vue';
import ServiceGalleryTile from './ServiceGalleryTile.vue';
import ServiceAmenitiesTile from './ServiceAmenitiesTile.vue';
import ServiceCheckinTile from './ServiceCheckinTile.vue';
import ServiceMushroomsTile from './ServiceMushroomsTile.vue';
import ServiceVideoTile from './ServiceVideoTile.vue';
import ServiceCipatTile from './ServiceCipatTile.vue';
import ServiceAddressTile from './ServiceAddressTile.vue';
import ServiceDirectionsTile from './ServiceDirectionsTile.vue';
import ServiceRulesTile from './ServiceRulesTile.vue';
import BookingPickerTile from './BookingPickerTile.vue';
import ServiceExcerptTile from './ServiceExcerptTile.vue';
import ServiceDescriptionTile from './ServiceDescriptionTile.vue';
import ServiceClubTile from './ServiceClubTile.vue';
import ServiceRelatedTile from './ServiceRelatedTile.vue';
import KillNextPrevTile from './KillNextPrevTile.vue';
import LangSwitcherTile from './LangSwitcherTile.vue';
import ServiceSearchTile from './ServiceSearchTile.vue';
import ServiceResultsTile from './ServiceResultsTile.vue';
import HostcardTile from './HostcardTile.vue';
import LiveSearchTile from './LiveSearchTile.vue';
import ShatteredImageTile from './ShatteredImageTile.vue';
import ProGalleryTile from './ProgalleryTile.vue';
import PdfViewerTile from './PdfViewerTile.vue';
import PdfProTile from './PdfProTile.vue';
import StarratingTile from './StarratingTile.vue';
import IconlistTile from './IconlistTile.vue';
import AnimatedheadingTile from './AnimatedheadingTile.vue';
import TocTile from './TocTile.vue';
import LottieTile from './LottieTile.vue';
import SharebuttonsTile from './SharebuttonsTile.vue';
import ScrollprogressTile from './ScrollprogressTile.vue';
import NewstickerTile from './NewstickerTile.vue';
import HotspotTile from './HotspotTile.vue';
import LoginformTile from './LoginformTile.vue';
import VideoplaylistTile from './VideoplaylistTile.vue';
import TextpathTile from './TextpathTile.vue';

import ChartTile from './ChartTile.vue';
import AudioTile from './AudioTile.vue';
import ShapedividerTile from './ShapedividerTile.vue';
import CountercircleTile from './CountercircleTile.vue';
import PostmetaTile from './PostmetaTile.vue';
import RelatedpostsTile from './RelatedpostsTile.vue';
import SoundcloudTile from './SoundcloudTile.vue';
import TagcloudTile from './TagcloudTile.vue';
import WpcommentsTile from './WpcommentsTile.vue';
import PaginationTile from './PaginationTile.vue';
import CarouselTile from './CarouselTile.vue';
import AuthorboxTile from './AuthorboxTile.vue';
import ViewscounterTile from './ViewscounterTile.vue';
import MenuanchorTile from './MenuanchorTile.vue';
import OsmmapTile from './OsmmapTile.vue';
import InstagramTile from './InstagramTile.vue';
import FacebookpageTile from './FacebookpageTile.vue';
import TwitterfeedTile from './TwitterfeedTile.vue';
import PostnavigationTile from './PostnavigationTile.vue';
import PricelistTile from './PricelistTile.vue';
import ProgresstrackerTile from './ProgresstrackerTile.vue';
import SitemapTile from './SitemapTile.vue';
import LinkinbioTile from './LinkinbioTile.vue';
import ShortcodeTile from './ShortcodeTile.vue';
import TemplateembedTile from './TemplateembedTile.vue';
import PaymentbuttonsTile from './PaymentbuttonsTile.vue';
import TextmaskTile from './TextmaskTile.vue';
import BlendtextTile from './BlendtextTile.vue';
import PagetitlebarTile from './PagetitlebarTile.vue';
import PortfolioTile from './PortfolioTile.vue';
import TextBlockTile from './TextBlockTile.vue';
import QueryloopTile from './QueryloopTile.vue';
import ReadingtimeTile from './ReadingtimeTile.vue';
import DarkmodeTile from './DarkmodeTile.vue';
import LightboxTile from './LightboxTile.vue';
import FloatingpanelTile from './FloatingpanelTile.vue';
import MobilebarTile from './MobilebarTile.vue';

// WooCommerce tiles
import WooProductsTile from './WooProductsTile.vue';
import WooPriceTile from './WooPriceTile.vue';
import WooMinicartTile from './WooMinicartTile.vue';
import WooAddtocartTile from './WooAddtocartTile.vue';
import WooCategoriesTile from './WooCategoriesTile.vue';
import WooRatingTile from './WooRatingTile.vue';
import WooProductTabsTile from './WooProductTabsTile.vue';
import WooRelatedTile from './WooRelatedTile.vue';
import WooUpsellsTile from './WooUpsellsTile.vue';
import WooCartTile from './WooCartTile.vue';
import WooCheckoutTile from './WooCheckoutTile.vue';
import WooProductTitleTile from './WooProductTitleTile.vue';
import WooProductImageTile from './WooProductImageTile.vue';
import WooProductDescriptionTile from './WooProductDescriptionTile.vue';
import WooProductMetaTile from './WooProductMetaTile.vue';
import WooProductStockTile from './WooProductStockTile.vue';
import WooOrderTrackingTile from './WooOrderTrackingTile.vue';
import WooBreadcrumbsTile from './WooBreadcrumbsTile.vue';
import WooNoticesTile from './WooNoticesTile.vue';
import WooProductNavigationTile from './WooProductNavigationTile.vue';
import WooSaleBadgeTile from './WooSaleBadgeTile.vue';
import WooCrossSellsTile from './WooCrossSellsTile.vue';
import WooRecentlyViewedTile from './WooRecentlyViewedTile.vue';
import WooProductBundleTile from './WooProductBundleTile.vue';
import WooProductFilterTile from './WooProductFilterTile.vue';
import WooQuickviewTile from './WooQuickviewTile.vue';
import WooWishlistTile from './WooWishlistTile.vue';
import WooProductGallerySliderTile from './WooProductGallerySliderTile.vue';
import WooCheckoutMultistepTile from './WooCheckoutMultistepTile.vue';
import WooMyaccountTile from './WooMyaccountTile.vue';
import WooComparisonTile from './WooComparisonTile.vue';

// Olo Space tiles
import OloRoomHeroTile from './OloRoomHeroTile.vue';
import OloRoomGalleryTile from './OloRoomGalleryTile.vue';
import OloRoomDescriptionTile from './OloRoomDescriptionTile.vue';
import OloRoomEquipmentTile from './OloRoomEquipmentTile.vue';
import OloRoomHoursTile from './OloRoomHoursTile.vue';
import OloRoomContactsTile from './OloRoomContactsTile.vue';
import OloRoomMapTile from './OloRoomMapTile.vue';
import OloRoomShareTile from './OloRoomShareTile.vue';
import OloRoomRelatedTile from './OloRoomRelatedTile.vue';
import OloRoomBadgesTile from './OloRoomBadgesTile.vue';
import OloRoomStatusTile from './OloRoomStatusTile.vue';
import OloRoomPricingTile from './OloRoomPricingTile.vue';
import OloRoomRulesTile from './OloRoomRulesTile.vue';
import OloRoomLocationTile from './OloRoomLocationTile.vue';
import OloRoomAvailabilityTile from './OloRoomAvailabilityTile.vue';
import OloRoomClosuresTile from './OloRoomClosuresTile.vue';
import OloRoomCalendarTile from './OloRoomCalendarTile.vue';
import OloRoomGridTile from './OloRoomGridTile.vue';
import OloRoomBookingTile from './OloRoomBookingTile.vue';
import OloRoomDashboardTile from './OloRoomDashboardTile.vue';
import OloRoomInfoTile from './OloRoomInfoTile.vue';
import OloRoomFeaturedTile from './OloRoomFeaturedTile.vue';
import OloRoomSearchTile from './OloRoomSearchTile.vue';
import OloRoomCategoriesTile from './OloRoomCategoriesTile.vue';
import OloRoomDistrictsTile from './OloRoomDistrictsTile.vue';
import OloRoomStatsPublicTile from './OloRoomStatsPublicTile.vue';
import OloRoomCtaTile from './OloRoomCtaTile.vue';

// Real Estate tiles (OLO Booking)
import PropertyGridTile from './PropertyGridTile.vue';
import PropertySearchTile from './PropertySearchTile.vue';
import PropertyMapTile from './PropertyMapTile.vue';
import PropertyCardTile from './PropertyCardTile.vue';
import PropertyFeaturedTile from './PropertyFeaturedTile.vue';
import PropertyStatsTile from './PropertyStatsTile.vue';
import PropertyCtaTile from './PropertyCtaTile.vue';
import PropertyHeroTile from './PropertyHeroTile.vue';
import PropertyInfoTile from './PropertyInfoTile.vue';
import PropertyGalleryTile from './PropertyGalleryTile.vue';
import PropertyDescriptionTile from './PropertyDescriptionTile.vue';
import PropertyAddressTile from './PropertyAddressTile.vue';
import PropertyFeaturesTile from './PropertyFeaturesTile.vue';
import PropertyPriceTile from './PropertyPriceTile.vue';
import PropertySpecsTile from './PropertySpecsTile.vue';
import PropertyMapSearchTile from './PropertyMapSearchTile.vue';
import PropertyContactFormTile from './PropertyContactFormTile.vue';
import PropertyHeroScrollTile from './PropertyHeroScrollTile.vue';
import PropertyVideoTile from './PropertyVideoTile.vue';
import PropertyExcerptTile from './PropertyExcerptTile.vue';
import PropertyRulesTile from './PropertyRulesTile.vue';

// OLO Booking vertical tiles
import RentalInventoryTile from './RentalInventoryTile.vue';
import EventListTile from './EventListTile.vue';
import AppointmentGridTile from './AppointmentGridTile.vue';
import RestaurantOpeningHoursTile from './RestaurantOpeningHoursTile.vue';
import RestaurantBookingFormTile from './RestaurantBookingFormTile.vue';

import ExternalTilePlaceholder from './ExternalTilePlaceholder.vue';

const props = defineProps({
  tile: { type: Object, required: true },
});

const tileComponents = {
  section: SectionTile,
  column: ColumnTile,
  hero: HeroTile,
  'hero-split': HeroSplitTile,
  audiohero: AudioHeroTile,
  'section-header': SectionHeaderTile,
  'info-cards': InfoCardsTile,
  worklist: WorkListTile,
  workgrid: WorkGridTile,
  statstrip: StatStripTile,
  hoursstrip: HoursStripTile,
  hoverlist: HoverListTile,
  lookbookmixer: LookbookMixerTile,
  categoryrail: CategoryRailTile,
  beforeafter: BeforeAfterTile,
  tripfinder: TripFinderTile,
  maskedvideohero: MaskedVideoHeroTile,
  searchhero: SearchHeroTile,
  smearhero: SmearHeroTile,
  photocover: PhotoCoverTile,
  masthead: MastheadTile,
  matchfixtures: MatchFixturesTile,
  showcasegrid: ShowcaseGridTile,
  productgrid: ProductGridTile,
  announcementbar: AnnouncementBarTile,
  introsplit: IntroSplitTile,
  mediacta: MediaCtaTile,
  imagehero: ImageHeroTile,
  glowhero: GlowHeroTile,
  terminalhero: TerminalHeroTile,
  oloxnav: OloxNavTile,
  oloxhero: OloxHeroTile,
  oloxmarquee: OloxMarqueeTile,
  oloxcards: OloxCardsTile,
  oloxsticky: OloxStickyTile,
  oloxpricing: OloxPricingTile,
  oloxstatement: OloxStatementTile,
  oloxlist: OloxListTile,
  oloxlessons: OloxLessonsTile,
  oloxquiz: OloxQuizTile,
  oloxbanner: OloxBannerTile,
  oloxfoot: OloxFootTile,
  oloxpagefx: OloxPageFxTile,
  oloxmanual: OloxManualTile,
  oloxpanel: OloxPanelTile,
  oloxrail: OloxRailTile,
  oloxscene: OloxSceneTile,
  coverdots: CoverdotsTile,
  bottombar: BottombarTile,
  pagelight: PagelightTile,
  oloxhome: OloxHomeTile,
  producthero: ProductHeroTile,
  northvideohero: NorthVideoHeroTile,
  northquoteslider: NorthQuoteSliderTile,
  featuredstory: FeaturedStoryTile,
  glowgallery: GlowGalleryTile,
  chathero: ChatHeroTile,
  'product-cards': ProductCardsTile,
  'step-timeline': StepTimelineTile,
  'process-steps': ProcessStepsTile,
  'cta-banner': CtaBannerTile,
  'trust-strip': TrustStripTile,
  projector: ProjectorTile,
  finder: FinderTile,
  builder: BuilderTile,
  mixer: MixerTile,
  schedule: ScheduleTile,
  hotspots: HotspotsTile,
  scaler: ScalerTile,
  timezone: TimezoneTile,
  availability: AvailabilityTile,
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
  badge: BadgeTile,
  team: TeamTile,
  accordion: AccordionTile,
  icontabs: IconTabsTile,

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
  megamenu: MegaMenuTile,
  oloheader: OloHeaderTile,
  'inner-columns': InnerColumnsTile,
  timeline: TimelineTile,
  flipcard: FlipCardTile,
  imgcompare: ImgCompareTile,
  marquee: MarqueeTile,
  togglebtn: ToggleBtnTile,
  form: FormTile,
  calendar: CalendarTile,
  booking: BookingTile,
  servicelist: ServiceListTile,
  serviceinfo: ServiceInfoTile,
  servicehero: ServiceHeroTile,
  servicestats: ServiceStatsTile,
  serviceprices: ServicePricesTile,
  servicegallery: ServiceGalleryTile,
  serviceamenities: ServiceAmenitiesTile,
  servicecheckin: ServiceCheckinTile,
  servicemushrooms: ServiceMushroomsTile,
  servicevideo: ServiceVideoTile,
  servicecipat: ServiceCipatTile,
  serviceaddress: ServiceAddressTile,
  servicedirections: ServiceDirectionsTile,
  servicerules: ServiceRulesTile,
  bookingpicker: BookingPickerTile,
  serviceexcerpt: ServiceExcerptTile,
  servicedescription: ServiceDescriptionTile,
  serviceclub: ServiceClubTile,
  servicerelated: ServiceRelatedTile,
  killnextprev: KillNextPrevTile,
  langswitcher: LangSwitcherTile,
  servicesearch: ServiceSearchTile,
  serviceresults: ServiceResultsTile,
  hostcard: HostcardTile,
  livesearch: LiveSearchTile,
  shatteredimage: ShatteredImageTile,
  progallery: ProGalleryTile,
  pdfviewer: PdfViewerTile,
  pdfpro: PdfProTile,
  starrating: StarratingTile,
  iconlist: IconlistTile,
  animatedheading: AnimatedheadingTile,
  toc: TocTile,
  lottie: LottieTile,
  sharebuttons: SharebuttonsTile,
  scrollprogress: ScrollprogressTile,
  newsticker: NewstickerTile,
  hotspot: HotspotTile,
  loginform: LoginformTile,
  videoplaylist: VideoplaylistTile,
  textpath: TextpathTile,

  chart: ChartTile,
  audio: AudioTile,
  shapedivider: ShapedividerTile,
  countercircle: CountercircleTile,
  postmeta: PostmetaTile,
  relatedposts: RelatedpostsTile,
  soundcloud: SoundcloudTile,
  tagcloud: TagcloudTile,
  wpcomments: WpcommentsTile,
  pagination: PaginationTile,
  carousel: CarouselTile,
  authorbox: AuthorboxTile,
  viewscounter: ViewscounterTile,
  menuanchor: MenuanchorTile,
  osmmap: OsmmapTile,
  instagram: InstagramTile,
  facebookpage: FacebookpageTile,
  twitterfeed: TwitterfeedTile,
  postnavigation: PostnavigationTile,
  pricelist: PricelistTile,
  progresstracker: ProgresstrackerTile,
  sitemap: SitemapTile,
  linkinbio: LinkinbioTile,
  shortcode: ShortcodeTile,
  templateembed: TemplateembedTile,
  paymentbuttons: PaymentbuttonsTile,
  textmask: TextmaskTile,
  blendtext: BlendtextTile,
  pagetitlebar: PagetitlebarTile,
  portfolio: PortfolioTile,
  'text-block': TextBlockTile,
  queryloop: QueryloopTile,
  readingtime: ReadingtimeTile,
  darkmode: DarkmodeTile,
  lightbox: LightboxTile,
  floatingpanel: FloatingpanelTile,
  mobilebar: MobilebarTile,

  // WooCommerce tiles
  woo_products: WooProductsTile,
  woo_price: WooPriceTile,
  woo_minicart: WooMinicartTile,
  woo_addtocart: WooAddtocartTile,
  woo_categories: WooCategoriesTile,
  woo_rating: WooRatingTile,
  woo_product_tabs: WooProductTabsTile,
  woo_related: WooRelatedTile,
  woo_upsells: WooUpsellsTile,
  woo_cart: WooCartTile,
  woo_checkout: WooCheckoutTile,
  woo_product_title: WooProductTitleTile,
  woo_product_image: WooProductImageTile,
  woo_product_description: WooProductDescriptionTile,
  woo_product_meta: WooProductMetaTile,
  woo_product_stock: WooProductStockTile,
  woo_order_tracking: WooOrderTrackingTile,
  woo_breadcrumbs: WooBreadcrumbsTile,
  woo_notices: WooNoticesTile,
  woo_product_navigation: WooProductNavigationTile,
  woo_sale_badge: WooSaleBadgeTile,
  woo_cross_sells: WooCrossSellsTile,
  woo_recently_viewed: WooRecentlyViewedTile,
  woo_product_bundle: WooProductBundleTile,
  woo_product_filter: WooProductFilterTile,
  woo_quickview: WooQuickviewTile,
  woo_wishlist: WooWishlistTile,
  woo_product_gallery_slider: WooProductGallerySliderTile,
  woo_checkout_multistep: WooCheckoutMultistepTile,
  woo_myaccount: WooMyaccountTile,
  woo_comparison: WooComparisonTile,

  // Olo Space tiles
  olo_room_hero: OloRoomHeroTile,
  olo_room_gallery: OloRoomGalleryTile,
  olo_room_description: OloRoomDescriptionTile,
  olo_room_equipment: OloRoomEquipmentTile,
  olo_room_hours: OloRoomHoursTile,
  olo_room_contacts: OloRoomContactsTile,
  olo_room_map: OloRoomMapTile,
  olo_room_share: OloRoomShareTile,
  olo_room_related: OloRoomRelatedTile,
  olo_room_badges: OloRoomBadgesTile,
  olo_room_status: OloRoomStatusTile,
  olo_room_pricing: OloRoomPricingTile,
  olo_room_rules: OloRoomRulesTile,
  olo_room_location: OloRoomLocationTile,
  olo_room_availability: OloRoomAvailabilityTile,
  olo_room_closures: OloRoomClosuresTile,
  olo_room_calendar: OloRoomCalendarTile,
  olo_room_grid: OloRoomGridTile,
  olo_room_booking: OloRoomBookingTile,
  olo_room_dashboard: OloRoomDashboardTile,
  olo_room_info: OloRoomInfoTile,
  olo_room_featured: OloRoomFeaturedTile,
  olo_room_search: OloRoomSearchTile,
  olo_room_categories: OloRoomCategoriesTile,
  olo_room_districts: OloRoomDistrictsTile,
  olo_room_stats_public: OloRoomStatsPublicTile,
  olo_room_cta: OloRoomCtaTile,

  // Real Estate tiles (OLO Booking)
  propertygrid: PropertyGridTile,
  propertysearch: PropertySearchTile,
  propertymap: PropertyMapTile,
  propertycard: PropertyCardTile,
  propertyfeatured: PropertyFeaturedTile,
  propertystats: PropertyStatsTile,
  propertycta: PropertyCtaTile,
  propertyhero: PropertyHeroTile,
  propertyinfo: PropertyInfoTile,
  propertygallery: PropertyGalleryTile,
  propertydescription: PropertyDescriptionTile,
  propertyaddress: PropertyAddressTile,
  propertyfeatures: PropertyFeaturesTile,
  propertyprice: PropertyPriceTile,
  propertyspecs: PropertySpecsTile,
  propertymapsearch: PropertyMapSearchTile,
  propertycontactform: PropertyContactFormTile,
  propertyheroscroll: PropertyHeroScrollTile,
  propertyvideo: PropertyVideoTile,
  propertyexcerpt: PropertyExcerptTile,
  propertyrules: PropertyRulesTile,

  // OLO Booking vertical tiles
  rentalinventory: RentalInventoryTile,
  eventlist: EventListTile,
  appointmentgrid: AppointmentGridTile,
  restaurantopeninghours: RestaurantOpeningHoursTile,
  restaurantbookingform: RestaurantBookingFormTile,

  // Clod — Evoluzione (studio editorial)
  studiohero: StudioHeroTile,
  filmreel: FilmReelTile,
  scrubtext: ScrubTextTile,
  themedemos: ThemeDemosTile,
  evonotes: EvoNotesTile,
};

const tileComponent = computed(() => {
  return tileComponents[props.tile.type] || ExternalTilePlaceholder;
});

const tileWrapStyle = computed(() => {
  if (props.tile.type === 'popup' && !props.tile.settings?.button_fullwidth) {
    return { display: 'inline-block' };
  }
  return {};
});
</script>
