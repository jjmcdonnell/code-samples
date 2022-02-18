let actions = {
  loadList ({ commit }, listName) {
    let response = false;
    switch(listName){
      case 'ArtThemes':{
        response = Api.MetaSelects.getArtThemes();
      }break;
      case 'Brands':{
        response = Api.MetaSelects.getBrands();
      }break;
      case 'Categories':{
        response = Api.MetaSelects.getCategories();
      }break;
      case 'Characters':{
        response = Api.MetaSelects.getCategories();
      }break;
      case 'Conditions':{
        response = Api.MetaSelects.getConditions();
      }break;
      case 'Cultures':{
        response = Api.MetaSelects.getCultures();
      }break;
      case 'DecorStyles':{
        response = Api.MetaSelects.getDecorStyles();
      }break;
      case 'Eras':{
        response = Api.MetaSelects.getEras();
      }break;
      case 'Franchises':{
        response = Api.MetaSelects.getFranchises();
      }break;
      case 'Materials':{
        response = Api.MetaSelects.getMaterials();
      }break;
      case 'Occasions':{
        response = Api.MetaSelects.getOccasions();
      }break;
      case 'Shapes':{
        response = Api.MetaSelects.getShapes();
      }break;
      case 'ManufacturerIdTypes':{
        response = Api.MetaSelects.getManufacturerIdTypes();
      }break;
    }
    commit('UPDATE_META_LIST', response.data, listName);
  },
}
export default actions;