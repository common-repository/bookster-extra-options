import{d as e,_ as t,r as o,R as a,a as n,b as r,Q as i,e as s,c as l}from"./QuantityInput-b8a39512.js";function c(){return e.useBookingStore()}function m(e,o){const a=c();return t.useStore(a,e,o)}const u=window.booksterMeta.appPath.bookster.assets+"/images/empty.png",b=o.forwardRef((function({className:e,extra:o,index:s,...l},m){const b=c();return a.createElement("div",{ref:m,...l,onClick:()=>function(e){const t={...e,isSelected:!e.isSelected,quantity:e.isSelected?0:1};b.setState((e=>{e.addonExtra.select[s]=t}))}(o),className:n.cx("bw-relative bw-flex bw-cursor-pointer bw-items-center bw-justify-between bw-gap-2 bw-rounded bw-border bw-border-solid bw-p-3 bw-shadow bw-transition bw-duration-300 bw-ease-in-out hover:bw-border-primary/60",o.isSelected&&"bw-border-primary bw-ring-1 bw-ring-primary/20 hover:bw-border-primary",e)},a.createElement("div",{className:"bw-flex bw-items-center bw-gap-2"},a.createElement("img",{className:"bw-aspect-square bw-h-14 bw-rounded bw-object-cover bw-object-center",src:o.model.transient_cover_url||u,alt:"Extra cover image"}),a.createElement("div",{className:"bw-flex bw-flex-col bw-gap-1"},a.createElement("h4",{className:"bw-text-base bw-font-semibold"},o.model.name),a.createElement("div",{className:"bw-flex bw-h-4 bw-flex-nowrap bw-items-center bw-space-x-2"},a.createElement("div",{className:"bw-inline-flex bw-items-center bw-justify-center bw-text-xs bw-text-base-foreground/80"},a.createElement(r.Clock,{className:"bw-mr-1 bw-h-3 bw-w-3"}),a.createElement("span",null,o.model.duration,"min")),a.createElement(n.Separator,{orientation:"vertical"}),a.createElement(n.Badge,{color:"base",variant:"fill"},a.createElement("span",null,t.formatPrice(o.model.price)))))),"enable"===o.model.quantity.activated&&o.model.quantity.max>1&&a.createElement(i,{quantity:o.quantity,decrement:()=>function(e){const t={...e,isSelected:!(e.quantity<2),quantity:e.quantity>0?e.quantity-1:e.quantity};b.setState((e=>{e.addonExtra.select[s]=t}))}(o),increment:()=>function(e){const t={...e,isSelected:0===e.quantity||e.isSelected,quantity:e.quantity<e.model.quantity.max?e.quantity+1:e.quantity};b.setState((e=>{e.addonExtra.select[s]=t}))}(o)}))}));function d(t){const o=m((e=>e.addonExtra.select)),r=e.useBookingLogic().process,i="desktop"===e.useBookingLayout().formLayout;return a.createElement("div",{...t},a.createElement(e.MainHeader,null),a.createElement(n.ScrollArea,{className:"btr-main-scrollarea"},i&&a.createElement(a.Fragment,null,a.createElement("div",{className:"bw-flex bw-h-14 bw-items-center bw-pl-4 bw-pr-2"},a.createElement("p",{className:"bw-mb-0 bw-flex-grow bw-font-heading bw-text-lg bw-font-semibold bw-text-primary"},r.computed.title)),a.createElement(n.Separator,null)),a.createElement(n.XyzTransitionGroup,{appear:!0,className:"bw-grid bw-gap-4 bw-p-4",xyz:"fade down-4 stagger-0.5"},o.map(((e,t)=>a.createElement(b,{key:e.model.extra_id,index:t,extra:e}))))),a.createElement(e.MainFooter,{submitButton:a.createElement(n.Button,{key:"select-extras-submit",onClick:async function(){r.mutate.nextAct()}},"Confirm")}))}const w={selectExtras:"selectExtras"},p={select:[]};function x(e){return{...e,addonExtra:p}}function k(e){const t=e.process.steps.map((e=>"service"===e.name?{...e,acts:[...e.acts,w.selectExtras]}:e));return{...e,process:{...e.process,steps:t}}}function E(e,t,o,n){return n.process.computed.act===w.selectExtras?{...e,main:{key:"select-extras",node:a.createElement(d,null)}}:e}function f(t,o,a){var n;t.process.computed.act===w.selectExtras&&(t.process.computed.title="Select Service Extras");const r=a.addonExtra.select.filter((e=>!0===e.isSelected)).map((e=>({id:e.model.extra_id,title:e.model.name,unitPrice:s.Decimal.fromNumber(e.model.price),quantity:e.quantity,amount:s.Decimal.fromNumber(e.model.price)})));if(void 0===t.input.bookingDetailsValue)return t;if(t.input.bookingDetailsValue.booking.items=[null==(n=t.input.bookingDetailsValue)?void 0:n.booking.items[0],...r],t.input.bookingDetailsValue){const o=e.calculateDetails(t.input.bookingDetailsValue);t.input.bookingDetailsValue=o}return void 0!==t.input.bookingMetaInput&&(t.input.bookingMetaInput.extraBookingItems=r.map((e=>({id:e.id,title:e.title,unitPrice:e.unitPrice.toNumber(),quantity:e.quantity,amount:e.amount.toNumber()})))),t}function g(e,t,o,{newPhase:a,oldPhase:n,oldActName:r,newActName:i}){return(a!==n&&"complete"===n||r===w.selectExtras)&&(t.addonExtra=p),0===t.addonExtra.select.length&&i===w.selectExtras||e}function y(e,t,o,{newActName:a}){var n;if(a!==w.selectExtras)return e;const r=window.booksterActiveExtras,i=null==(n=t.select.service)?void 0:n.service_id,s=i?r.filter((e=>!e.unavailable_services.includes(i))):r;return 0===s.length||(t.addonExtra.select=s.map((e=>({model:e,isSelected:!1,quantity:0}))),!1)}function S(e,t){const o=t.addonExtra.select;if(null===e)return e;const a=e.duration+o.reduce((function(e,t){return t.isSelected?e+t.model.duration*t.quantity:e}),0);return{...e,duration:a}}function N(){const e=m((e=>e.addonExtra)),t=o.useMemo((()=>e.select.filter((e=>e.isSelected))),[e.select]);return t.length>0&&a.createElement("p",{className:"bw-flex bw-w-full bw-items-center bw-justify-between bw-gap-2"},a.createElement("span",{className:"bw-text-xs bw-uppercase bw-text-base-foreground/60"},"Extra"),a.createElement("span",{className:"bw-truncate"},t.map(((e,o)=>a.createElement(a.Fragment,null,e.model.name," ",e.quantity>1&&"(x"+e.quantity+")",o+1!==t.length&&", ")))))}l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.bookingConfig,"bookster-extra-options",k,30),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.bookingParts,"bookster-extra-options",E),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.bookingInitState,"bookster-extra-options",x),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.bookingLogic,"bookster-extra-options",f,30),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.mutatePrevAct,"bookster-extra-options",g,90),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.mutateNextAct,"bookster-extra-options",y,90),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.bookingDuration,"bookster-extra-options",S),l.booksterHooks.addFilter(l.HOOK_NAMES.bookingForm.SummarySelectAfterService,"bookster-extra-options",(e=>[...e,a.createElement(N,null)]),20);